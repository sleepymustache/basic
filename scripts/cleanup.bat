@echo off

cd ..

echo Cleaning up dist folder...
del /f /s dist


REM del /F /S src/app/template/*
REM del /F src/images
REM del /F src/scss/partials
REM del /F src/favicon.ico
REM del /F src/facicon.png
REM del /F src/index.php
REM del /F src/manifest.json
REM
REM echo // Styles for forms        > src/scss/partials/_forms.scss
REM echo // Styles for Navigation   > src/scss/partials/_navigation.scss
REM echo // Styles for Structure    > src/scss/partials/_structure.scss
REM echo require_once(((@include_once('Webkit/init.php')) ? $docRoot : $_SERVER['DOCUMENT_ROOT']) . '/app/core/sleepy.php'); > src/index.php


echo Done!
pause