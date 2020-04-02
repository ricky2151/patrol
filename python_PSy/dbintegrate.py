from datetime import datetime

import time
import mqtt as mqtt
import modulderive as md
import sqlite3

global startShiftTime
startShiftTime={}

def konversi(jam):
      ptr=conn.execute("select id,start from  times ")
      for n in ptr:
            convert_id=(str(n[0]))
            convert_jam=(str(n[1][0:2]))
            if(jam in convert_jam):
                  return convert_id

###! ambil timeshift
def timeShift(mode="start"):
   dictShiftTime={}
   cursor = conn.execute("select id, start, end from times ")
   if(mode=="start"): #startshift 
      for t in cursor:
         key = t[0]
         val = str(t[1])[0:2]
         dictShiftTime.update({key:val})
      return dictShiftTime
   else:
      for t in cursor:
         key = t[0]
         val = str(t[2])[0:2]
         dictShiftTime.update({key:val})
      return dictShiftTime


###!Ambil semua shift yang berada pada waktu dan tanggal aktif
def sendShiftQRCode(idTimes,date):
   cursor = conn.execute(("select u.name, s.id, u.master_key, s.time_id, t.start, s.room_id, s.date from users as u, times as t, shifts as s where t.id=s.time_id AND u.id=s.user_id AND s.time_id=%s AND s.date="+"'"+"%s"+"'")%(idTimes,date))
   for e in cursor:
      print("+++++++++++++++++++++++++++++++++++++++++")
      strQuery = str("{} # {} # {} # {} # {} # {} # {}".format(e[0],e[1],e[2],e[3],e[4],e[5],e[6]))
      print(strQuery)
      shiftId = str(e[1])
      masterKeyUser = str(e[2])
      room = str(e[5])

      qrCodeData=md.enkrip(shiftId,masterKeyUser)
      md.dekrip(qrCodeData,shiftId,masterKeyUser)
      print(qrCodeData)
      topic='ruang/'+room
      print("kirim ke "+topic)
      
      ##!kirim ke stiap node sesuai room_id
      mqtt.publishData(topic,qrCodeData)
      print("+++++++++++++++++++++++++++++++++++++++++")
      time.sleep(1)

#__MAIN
#create connection to broker MQTT
mqtt.createConnection("broker.shiftr.io",1883,60,"SERVER","904e4807","cfdc8ca761caadf9")
#create connection to database
conn = sqlite3.connect('C:/xampp/htdocs/patrol/laravelweb_PSy/database/satpam.sq3')
print ("Opened database successfully")
print ("==========================================")


bStart = False
#get seluruh waktu awal shift 
startShiftTime=timeShift("start")
endShiftTime=timeShift("end")
count=0
while(True):
   current = datetime.now()
   
   if(not bStart): 
      newMinute = current.minute
      oldMinute = newMinute - 1
      bStart = True
   
   if(newMinute < oldMinute): newMinute = 60 + current.minute
   else: newMinute = current.minute

   if(newMinute-oldMinute >= 1):
      print ("==========================================")
      print ("scanning time"+str(current))
      oldMinute = newMinute
      mqtt.createConnection("broker.shiftr.io",1883,60,"SERVER","904e4807","cfdc8ca761caadf9")
      
      currentHour = ("{:02d}".format(current.hour))
      currentDate = ("{:04d}-{:02d}-{:02d}".format(current.year,current.month,current.day))
      print("Jam Sekarang: ",currentHour)
      print("date = "+currentDate)
            
      #check currentHour in range start-end time shift and convert to id timeShift
      for n in range(1,len(startShiftTime)+1):
         if(currentHour>=startShiftTime[n] and currentHour<endShiftTime[n]):
            sendShiftQRCode(dict((v,k) for k,v in startShiftTime.items()).get(startShiftTime[n]),currentDate)  
            break
         
      print ("==========================================")
      
      
  