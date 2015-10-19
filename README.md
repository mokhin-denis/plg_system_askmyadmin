# AskMyAdmin
AskMyAdmin prevent login to back-end of site till entering correct key=value pair. This is an extended version of plg_backendtoken plugin.
 
Main idea of this plug-in - to prevent login to administrator's panel by using standard URL. It will hide your admin part of site.
 
## Supported languages
![en](http://my-j.ru/media/mod_languages/images/en.gif) English (en-GB) by Denis Mokhin

![ru](http://my-j.ru/media/mod_languages/images/ru.gif) Russian (ru-RU) by Denis E Mokhin

![it](http://my-j.ru/media/mod_languages/images/it.gif) Italian (it-IT) by Marco Surian

## Usage
1. Download latest version of plug-in.
2. Install plug-in, using Extensions - Extension Manager.
3. Make base settings of plug-in, using Extensions - Plug-in Manager.
    1. Define your keyname
    2. Define your keyvalue
    3. Define URL for redirect
    4. Activate plug-in

## Example
You have http://mysite.com site. Standard login to admin panel is http://mysite.com/administrator. When you install and activate AskMy plug-in for correct login you should use this link: http://mysite.com/administrator/?keyname=keyvalue