# rnk2csv
# @author Juha-Matti Santala / @hamatti
# Transforms FTHA .rnk score files to csv for better 3rd party usage

# Usage: python rnk2csv --in [in_file] --out [out_file]

import argparse
import csv
import os.path
import urllib
import sys

parser = argparse.ArgumentParser(description="Transform FTHA .rnk scores to .csv")
parser.add_argument('--in', dest='infile', type=str, help='Input file')
parser.add_argument('--url', dest='inurl', type=str, help='Input url')
parser.add_argument('--out', dest='outfile', type=str, help='Output file')

args = parser.parse_args()

if not (args.infile or args.inurl) and not args.outfile:
	print "You need to define input and output files"
	sys.exit(1)

if args.infile and os.path.isfile(args.infile):
	ranking = open(args.infile, 'r').readlines()
elif args.inurl:
	ranking = urllib.urlopen(args.inurl).readlines()

csv_out = csv.writer(open(args.outfile, 'w'))
ranking = [line.strip() for line in ranking if not line.startswith('-')][1:]

for line in ranking:
	sp = line.split('  ')
	sp = [f for f in sp if f][1:]
	sp[0] = sp[0].strip()
	sp[1:] = [int(s) for s in sp[1:]]
	csv_out.writerow(sp)

