@echo OFF
REM This is the file that is used to send all used javascript files to the 
REM documentation generator.  It takes all .js files that are necessary and
REM uses the JSDoc in them to generate documentation as .html files and outputs
REM them to ../doc for easy viewing.
set jsdoc=%code%\jsdoc-toolkit\jsdoc-toolkit
java -jar "%jsdoc%\jsrun.jar" "%jsdoc%\app\run.js" ^
        -t="%jsdoc%\templates\jsdoc" -d="..\doc\js" -a -p tasklist.js wizard.js
