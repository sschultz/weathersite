from urllib import request
from sys import argv
import json

if len(argv) != 3:
	print("   Usage: python {} [CamIPListFile] [OutputWorkingURLs]".format(argv[0]))
	exit()

#read the list of available cameras URLs that are known
cams = list(open(argv[1], 'r'))
if len(cams) == 0:
	print("Unable to read Camera IP List File")
	exit()

#list of working cameras to be appended
working = []

#check if each cam is working
for url in cams:
	url = url.strip()
	try:
		request.urlopen(url, timeout=5)
		working.append(url)
		
	except:
		print(url, 'not working')

#write our results to fout
fout = open(argv[2], 'w')
json.dump(working,fout)
fout.close()