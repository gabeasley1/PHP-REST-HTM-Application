@echo OFF
"%code%\closure-library\closure\bin\calcdeps.py" -i jquery.js -i jquery-ui.js -i tasklist.js -p "%code%\closure-library" -o compiled -c "%code%\closure-library\compiler.jar" -f "--compilation_level=SIMPLE_OPTIMIZATIONS"
