@echo off
set /p newRepo="Enter a directory name: "
set /p SMtype="[B]asic or [R]outed: "

REM Create new REPO
REM ---------------

echo Creating the new Repo...

IF /I "%SMtype%"=="B" (
	git clone --depth 1 https://github.com/sleepymustache/basic.git %newRepo%
) ELSE (
	git clone --depth 1 https://github.com/sleepymustache/routed.git %newRepo%
)

cd %newRepo%

echo Installing sleepyMUSTACHE...
git checkout 2.x
git remote remove origin > nul
del /Q .git\refs\remotes\origin\HEAD > nul
git commit --amend -m "Installed sleepyMUSTACHE."

echo Optimizing repo...
git  gc --aggressive > nul

echo Creating develop branch...
git branch develop > nul
git checkout develop > nul

git submodule init
git submodule sync
git submodule update

echo Done.

code ./
exit