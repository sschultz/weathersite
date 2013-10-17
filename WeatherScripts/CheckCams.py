from urllib import request
from sys import argv
from csv import DictReader
from time import sleep
import json

if len(argv) != 3:
	print("   Usage: python {} [CamIPListFile] [OutputWorkingURLs]".format(argv[0]))
	exit()

cams = []
	
#read the list of available cameras that are known
try:
	f = open(argv[1], 'r')
	reader = DictReader(f)
	for camInfo in reader:
		#remove leading or trailing spaces
		url = camInfo['URL'].strip()
		#check if each cam is working before adding them to the output list
		try:
			request.urlopen(url, timeout=5)
			cams.append(camInfo)
		except:
			print('WARNING:', camInfo['Desc'], '('+camInfo['URL']+')', 'Not Responding!!!')
	
	f.close()
except:
	print("ERROR: Unable to read Camera CSV File")
	sleep(10)
	exit()

#write our results to fout
fout = open(argv[2], 'w')
json.dump(cams,fout)
fout.close()
sleep(10)