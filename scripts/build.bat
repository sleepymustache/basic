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
echo +-------------------------+
echo - Building dist folder... -
echo +-------------------------+
echo[
CALL npm run build

echo[
echo +-------+
echo - Done! -
echo +-------+
echo[
choice /N /C C /D C /T 100 /M "Press 'c' to [C]lose."