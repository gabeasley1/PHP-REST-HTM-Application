@echo off
%CLOSURE%\closure-library\closure\bin\calcdeps.py -i jquery.min.js -i jquery-ui.min.js -i tasklist.js -p %CLOSURE%\closure-library\closure -o compiled -c compiler.jar -f "--compilation_level=SIMPLE_OPTIMIZATIONS"
