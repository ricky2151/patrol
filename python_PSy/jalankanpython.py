#!/usr/bin/env python
from datetime import datetime

import time
import mqtt as mqtt

#__MAIN
#create connection to broker MQTT
print("halo")
mqtt.createConnection("broker.shiftr.io",1883,60,"SERVER","samuelricky-skripsi-coba","sukukata123")
#create connection to database
mqtt.publishData('hayoo','DATA KE 2')

