@echo OFF
REM This is just file that I use to compile my javascript more succinctly 
REM without having to type this huge line every time.  Adjust your files
REM accordingly, if needed.
REM
REM Required: 
REM   = Python (2.5 < version < 3.x) http://www.python.org/getit/releases/2.7.2/
REM   = Closure Library http://code.google.com/p/closure-library/source/checkout
REM   = Closure Compiler Jar 
REM         http://code.google.com/closure/compiler/docs/gettingstarted_app.html
REM   = jQuery (tested with 1.6.2) 
REM         http://docs.jquery.com/Downloading_jQuery#Current_Release
REM   = jQuery UI (tested with 1.8.14) http://jqueryui.com/download
"%code%\closure-library\closure\bin\calcdeps.py" ^
        -i tasklist.js -p "%code%\closure-library" -o compiled ^
        -c "%code%\closure-library\compiler.jar" ^
        -f "--compilation_level=ADVANCED_OPTIMIZATIONS"
