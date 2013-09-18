REM NO SPACES BETWEEN THE VAR NAME AND THE PATH
SET WINDFARMCSV=H:\gsci.weather\WindFarm_TenMin.dat

cd C:\wamp\WeatherScripts
python LastCSV2Table.py WindFarm "%WINDFARMCSV%"