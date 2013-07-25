# -*- coding: UTF-8 -*-

import re

def isValid(latlng):
    strs = latlng.split(",", 1)
    if float(strs[0]) > 90.0 or float(strs[0]) < -90.0:
    #if float(strs[0]) > 31.345 or float(strs[0]) < 31.14:
        return False
    if float(strs[1]) > 180.0 or float(strs[1]) < -180.0:
    #if float(strs[1]) > 121.58 or float(strs[1]) < 121.36:
        return False
    return True

def process(inputFile, outputFile):
    fin = open(inputFile)
    fout = open(outputFile, "w")
    
    coordinatePattern = re.compile(r"-?\d{1,3}\.\d{6},\s*-?\d{1,3}\.\d{6}")

    isHead = True
    curCoordinates = ""

    for line in fin:
        
        if isHead:
            fout.write("id;category;store\n")
            isHead = False
            continue
        
        strs = line.strip().split(",", 2)

        if len(strs[2]) != 0:
            curCoordinates = ""
            coordinates = coordinatePattern.search(strs[2])
            if coordinates:
                tmpCoordinates = coordinates.group()
                if isValid(tmpCoordinates):
                    curCoordinates = tmpCoordinates.replace(' ', '')       
            continue
            
        if len(curCoordinates) != 0 and len(strs[1]) != 0:
            fout.write(line.strip().replace(",", ";") + curCoordinates + "\n")

    fout.close()
    fin.close()

process("udid-cate-gps-1000.csv", r"E:\apache-solr-3.6.2\example\exampledocs\udid-cate-gps-1000.csv")
