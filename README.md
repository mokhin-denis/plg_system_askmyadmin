# AskMyAdmin ![](https://img.shields.io/badge/joomla-3.x-yellow.svg?style=plastic) ![](https://img.shields.io/badge/joomla-4.x--alpha-blue.svg?style=plastic) #
AskMyAdmin prevent login to back-end of site till entering correct key=value pair. This is an extended version of plg_backendtoken plugin.
 
Main idea of this plug-in - to prevent login to administrator's panel by using standard URL. It will hide your admin part of site.

### This plug-in is ready for [Joomla! 4 Alpha](https://github.com/joomla/joomla-cms/tree/4.0-dev) ###
 
## Supported languages ##
![en](https://mokhin-tech.ru/media/mod_languages/images/en.gif) English (en-GB) by Denis Mokhin

![ru](https://mokhin-tech.ru/media/mod_languages/images/ru.gif) Russian (ru-RU) by Denis Mokhin

![it](https://mokhin-tech.ru/media/mod_languages/images/it.gif) Italian (it-IT) by Marco Surian

![pl](https://mokhin-tech.ru/media/mod_languages/images/pl.gif) Polish (pl-PL) by [darekmer](https://www.transifex.com/user/profile/darekmer/)

![fr](https://mokhin-tech.ru/media/mod_languages/images/fr.gif) French (fr-FR) by [Anthony Ceccarelli](https://www.stereonomy.com/)

You can improve current translation or add new languages. Please visit [transifex.com](https://www.transifex.com/mokhin/askmyadmin/)

## Usage ##
1. Download latest version of plug-in.
2. Install plug-in, using Extensions - Extension Manager.
3. Make base settings of plug-in, using Extensions - Plug-in Manager.
    1. Define your keyname
    2. Define your keyvalue
    3. Define URL for redirect
    4. Activate plug-in

## Example ##
You have http://mysite.com site. Standard login to admin panel is http://mysite.com/administrator. When you install and activate AskMy plug-in for correct login you should use this link: http://mysite.com/administrator/?keyname=keyvalue

## Vote ##
If you found this module useful, please visit my page on [JED](http://extensions.joomla.org/extensions/extension/access-a-security/site-security/askmyadmin) and post a reply. Thank you!

## Copyright ##
This plug-in is under GNU/GPL v2 or later; please visit http://www.gnu.org/licenses/old-licenses/gpl-2.0.html or see [LICENSE.txt](LICENSE.txt)