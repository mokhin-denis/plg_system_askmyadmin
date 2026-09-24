#!/usr/bin/env bash
set -Eeuo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
COMPOSE_FILE="$ROOT_DIR/compose.yaml"
KEEP_RUNNING="${KEEP_RUNNING:-0}"

log() {
  printf '[smoke] %s\n' "$*"
}

fail() {
  printf '[smoke] ERROR: %s\n' "$*" >&2
  exit 1
}

compose() {
  local project="$1"
  shift
  docker compose -f "$COMPOSE_FILE" -p "$project" "$@"
}

wait_for_http() {
  local port="$1"
  local attempts=60

  until curl --silent --show-error --fail "http://127.0.0.1:${port}/" >/dev/null; do
    attempts=$((attempts - 1))
    if (( attempts == 0 )); then
      return 1
    fi
    sleep 2
  done
}

configure_plugin() {
  local project="$1"
  local prefix
  local enabled

  prefix="$(compose "$project" exec -T joomla php -r 'require "configuration.php"; $config = new JConfig(); echo $config->dbprefix;' | tr -d '[:space:]')"
  [[ "$prefix" =~ ^[A-Za-z0-9_]+$ ]] || fail "Unexpected Joomla table prefix: $prefix"

  compose "$project" exec -T db mysql -ujoomla -pjoomla_password joomla -e "UPDATE ${prefix}extensions SET enabled = 1, params = 0x7b226b65796e616d65223a226b6579222c226b657976616c7565223a2276616c7565222c2275726c223a22227d WHERE type = 'plugin' AND folder = 'system' AND element = 'askmyadmin';" >/dev/null
  enabled="$(compose "$project" exec -T db mysql -N -B -ujoomla -pjoomla_password joomla -e "SELECT enabled FROM ${prefix}extensions WHERE type = 'plugin' AND folder = 'system' AND element = 'askmyadmin';" | tr -d '\r\n')"
  [[ "$enabled" == 1 ]] || fail "AskMyAdmin plugin is not enabled"
}

assert_redirect() {
  local port="$1"
  local query="$2"
  local expected="$3"
  local location

  location="$(curl --silent --show-error --dump-header - --output /dev/null --max-redirs 0 "http://127.0.0.1:${port}/administrator/${query}" | awk 'BEGIN{IGNORECASE=1} /^Location:/{sub(/\r$/, ""); sub(/^[^:]+:[[:space:]]*/, ""); print; exit}')"
  [[ "$location" == "$expected" ]] || fail "Expected Location '$expected', got '$location'"
}

run_case() {
  local name="$1"
  local base_image="$2"
  local image_tag="$3"
  local port="$4"
  local project="askmyadmin-${name}"
  export JOOMLA_BASE_IMAGE="$base_image"
  export ASKMYADMIN_IMAGE="askmyadmin/joomla:${image_tag}"
  export JOOMLA_PORT="$port"

  compose "$project" down --volumes --remove-orphans >/dev/null 2>&1 || true
  log "Building ${base_image} as askmyadmin/joomla:${image_tag}"
  compose "$project" build joomla
  compose "$project" up -d

  cleanup_case() {
    if [[ "$KEEP_RUNNING" != 1 ]]; then
      compose "$project" down --volumes --remove-orphans
    fi
  }
  trap cleanup_case RETURN

  log "Waiting for Joomla ${name}"
  wait_for_http "$port" || {
    compose "$project" logs joomla db >&2 || true
    fail "Joomla ${name} did not become ready"
  }

  log "Checking installed and enabled plugin on Joomla ${name}"
  configure_plugin "$project"

  log "Checking access barrier on Joomla ${name}"
  assert_redirect "$port" "" "http://127.0.0.1:${port}/"
  assert_redirect "$port" "?key=wrong" "http://127.0.0.1:${port}/"

  log "Checking valid key grants the current session on Joomla ${name}"
  local cookie_file
  cookie_file="$(mktemp)"
  curl --silent --show-error --dump-header /tmp/askmyadmin-valid-headers \
    --cookie-jar "$cookie_file" --max-redirs 0 \
    "http://127.0.0.1:${port}/administrator/?key=value" >/dev/null || true
  grep -Eiq '^Location:.*administrator/?' /tmp/askmyadmin-valid-headers \
    || fail "Valid key did not redirect back to administrator"
  curl --silent --show-error --fail --cookie "$cookie_file" \
    "http://127.0.0.1:${port}/administrator/" >/dev/null \
    || fail "Authorized session could not access administrator"
  rm -f "$cookie_file" /tmp/askmyadmin-valid-headers

  log "Joomla ${name}: passed"
}

run_case "joomla54" "joomla:5.4.8-php8.3-apache" "5.4.8-php8.3-apache" 8081
run_case "joomla61" "joomla:6.1.3-php8.3-apache" "6.1.3-php8.3-apache" 8082

log "All smoke tests passed"
