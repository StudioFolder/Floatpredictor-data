# see license.txt
# python getdatanomads2.py yyyymmdd tttt
import sys
import numpy as np
from scipy.io.netcdf import netcdf_file
import subprocess

t=[   0,    3,    6,    9,   12,   15,   18,   21,   24,
      27,   30,   33,   36,   39,   42,   45,   48,   51,
      54,   57,   60,   63,   66,   69,   72,   75,   78,
      81,   84,   87,   90,   93,   96,   99,  102,  105,
      108,  111,  114,  117,  120,  123,  126,  129,  132,
      135,  138,  141,  144,  147,  150,  153,  156,  159,
      162,  165,  168,  171,  174,  177,  180,  183,  186,
      189,  192,  195,  198,  201,  204,  207,  210,  213,
      216,  219,  222,  225,  228,  231,  234,  237,  240,
      252,  264,  276,  288,  300,  312,  324,  336,  348,
      360,  372,  384]
#print t
#lat=nc['lat'][:]
#lon=nc['lon'][:]
p=[1000,850,500,250,100,30,10]

date=sys.argv[1]
gfstime=sys.argv[2]
gfstime=gfstime[0:2]
print date,gfstime

fid=open("gfs.time","w")
fid.write("Hour since %s-%s-%sT%s:00:00Z\n"%(date[0:4],date[4:6],date[6:],gfstime))
#fid.write(date+" "+gfstime+"\n")
fid.close()

u=0
un=0
v=0
vn=0

def handle(tau,f0,f1):
    print "handle",tau,f0,f1
    framenum=int(tau/3+0.001)
    for lev in range(0,7):
        pr=p[lev]
        if f0 == 0:
            ul=un[0,lev,:,:]
            vl=vn[0,lev,:,:]
        else:
            ul=f0*u[0,lev,:,:]+f1*un[0,lev,:,:]
            vl=f0*v[0,lev,:,:]+f1*vn[0,lev,:,:]
        np.savez_compressed("gfs-%04d/uv-%04d.npz"%(pr,framenum),u=ul,v=vl)
      
def ingest(j):
    global un,vn
    cmd=["./nomads2",date,gfstime,"%03d"%t[j]]
    print cmd
    ret=subprocess.call(cmd)
    if ret:
        print "Data error"
        exit(1)
    nc0=netcdf_file("tmp.nc","r")
    nc=nc0.variables
    un=np.copy(nc['u'][:])
    vn=np.copy(nc['v'][:])
    if np.any(np.isnan(un)) or np.any(np.isnan(vn)):
        print "NaNs in velocities"
        exit(1)
        
def copyback():
    global u,v
    #  print "copyback"
    u=np.copy(un)
    v=np.copy(vn)

t0=-3.0;
tn=0.0;
j=0;
ingest(j)
for ts in np.arange(0,t[-1]+1,3):
    delt=tn-t0;
    handle(ts,float(tn-ts)/delt,float(ts-t0)/delt)
    if ts==t[-1]: break
    if ts==tn:
        j=j+1
        if t[j]-tn > 3: copyback()
        ingest(j)
        t0=tn
        tn=t[j]
        
