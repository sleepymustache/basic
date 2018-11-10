@echo off
cls
cd ..

echo[
echo +----------------------------+
echo - Installing NPM Packages... -
echo +----------------------------+
echo[
CALL npm install

echo[
echo +---------------------------+
echo - Zipping up dist folder... -
echo +---------------------------+
echo[
CALL npm run zip

echo[
echo +----------------+
echo - Cleaning up... -
echo +----------------+
echo[
@rd /s /q dist

echo[
echo +-------+
echo - Done! -
echo +-------+
echo[
choice /N /C C /D C /T 100 /M "Press 'c' to [C]lose."