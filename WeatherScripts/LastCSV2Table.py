import mysql.connector
from sys import argv

if len(argv) != 3:
	print('   Usage: python', argv[0], '[tablename] [csv data file path]')
	print('   Example: python', argv[0], '"WindFarm" "C:\\filepath\\datafile.dat"')
	print()
	print(' "PythonMySQLUser.txt" must exist in the current directory')
	print(' with 3 lines: Database, Username, Password')
	exit()

table = argv[1]
dataFile = argv[2]
	
#read database connection information
try:
	f = open('PythonMySQLUser.txt','r')
except:
	print('Unable to open "PythonMySQLUser.txt"')
	exit()

#read database name
database = f.readline()
#remove whitespace such as \n and tabs before and after string
database = database.strip()

#read connecting user name
username = f.readline()
username = username.strip()

#read connecting user password
passwd = f.readline()
passwd = passwd.strip()

f.close()

#read last line of csv file
try:
	f = open(dataFile, 'r')
except:
	print('Unable to open "'+dataFile+'"')
	exit()

#read the last 2000 characters to find the last line
flen = f.seek(0, 2)
f.seek(flen-2000)
tmp = f.readline()
while tmp.strip() != '': #tmp.strip() will be '' at end of file or last empty line
	last = tmp
	tmp = f.readline()

f.close()

sqlstr = 'INSERT INTO '+table+' VALUES ('+last+')'
#print(sqlstr)

try:
	con = mysql.connector.connect(user=username, passwd=passwd, db=database)
	
except:
	print('ERROR: Unable to connect to mysql database')
	exit()
	
#update table
cursor = con.cursor()
try:
	cursor.execute(sqlstr)
	print(cursor.rowcount, 'Row(s) Added')
	#Write data to database
	con.commit()

except mysql.connector.errors.Error as err:
	print('Unable to update table {}:'.format(table))
	print(err)

cursor.close()
con.close()