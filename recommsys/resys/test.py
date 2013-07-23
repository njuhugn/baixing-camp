import numpy as np
import formatter as fmt
from sklearn import cluster

k_means = cluster.MiniBatchKMeans()
results = []

def clustering(filename):

    print "formatting data..."
    fmt.process(filename, "tmp")
    
    print "loading data..."
    f = open("tmp")
    #data = np.loadtxt(f, delimiter=',', usecols=(1,2))
    data = np.loadtxt(f, delimiter=',', usecols=(0,1))
    f.close()

    print "building dist..."
    origin = {}
    f = open(filename)
    for line in f:
        strs = line.strip().split(',')
        origin[','.joint(strs[:2])] = [float(strs[1]),float(strs[2])]
        #print origin[strs[0]]
    f.close()

    print "clustering..."
    global k_means
    k_means = cluster.MiniBatchKMeans(n_clusters=10)
    k_means.fit(data)

    print "saving result..."
    global results
    for i in range(0,10):
        results.append([])
    for key,val in origin.items():
        c = k_means.predict(val)[0]
        results[c].append(key)
    #print results

def query(L=[32.38,121.04]):
    c = k_means.predict(L)[0]
    ret = results[c]
    print c
    print len(ret), "related users are found."
    return ret

#clustering("gps-origin.txt")
#query([32.38,121.04])
