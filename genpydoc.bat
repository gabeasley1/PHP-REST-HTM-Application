echo @OFF
set dir=%CD%
rmdir .\doc\php
cd "%code%\PhpDocumentor-1.4.3\PhpDocumentor"
phpdoc.bat -d "%code%\PHP-REST-HTM-Application" ^
    -t "%code%\PHP-REST-HTM-Application\doc\php" ^
    -dn "PHP-RestApplication" ^
    -dc "PHP REST Application" -s -pp
    -ti "PHP REST Application"
cd "%dir%"
rename.py
echo %CD%
echo %dir%
