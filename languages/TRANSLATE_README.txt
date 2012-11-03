How works:

To translate Open Classifieds to different languages we use gettext as many other software (like WordPress).
Before we were using plain text files, but it was very difficult to share these files and we had problems with encoding.
To do translations is very easy with the following GlotPress installation http://open-classifieds.com/translate/projects/open-classifieds.
If you would like to translate Open Classifieds to another language, please feel free to use the form in the bottom of this page. Another way to translate the .po files is using Poedit desktop software. If you use Poedit, please later share the .po file (using contact form). This would be a great help for the software.


How to translate:

All the available languages are included in the downloadable package.
Login in Translate if you need a user request it’s in the form below. (User: ‘translator’ and Password: ‘trans1234′)
Choose your language
Export to .po file
Rename the file to messages.po
Open the file with poedit, and click save.
You need to have 2 files; messages.po and messages .mo
Create a folder in /languages/ called xx_XX where xx is the language, ex: es_ES
Inside this folder create another one named LC_MESSAGES
At the end you need to have something like /openclassifieds/languages/es_ES/LC_MESSAGES/ here both files .po .mo
Go to your admin in OC, Settings->Basic Configuration->Language , click save
Your OC installation should now be in the new language.
To translate the emails:
Open folder /content/email/
Make a copy of en_EN
Rename it to the language you want for example nl_NL
Edit the file template.html
Translate the words to your language



Troubleshooting:

Be sure your hosting has the locales (you can check in unix with command locales -a) if not the site will not use the language you choose.
You need the .mo file, this is the one that really matters to the system.
You might need to change the HTML charset or the collation for your DB depending on your locale.
Check the permissions for the files .po .mo the should have 755.
For more help please ask in the forum.
