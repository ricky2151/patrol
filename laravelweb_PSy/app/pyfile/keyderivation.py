
#import dependencies
import os
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC
from cryptography.hazmat.backends import default_backend

string = "Python is interesting."

# string with encoding 'utf-8'
arr = bytes(string, 'utf-8')
print(arr)

#make a key
backend = default_backend()
# Salts should be randomly generated
salt = b"3105201908:00shift3" #<tanggal ddmmyyyy><hh:mm>shift<no3>
#salt = os.urandom(16)

# derive
kdf = PBKDF2HMAC(
     algorithm=hashes.SHA256(),
     length=32,
     salt=salt,
     iterations=100000,
     backend=backend
)
key = kdf.derive(b"164e417kuncoro") #<nomor induk satpam><nama satpam>
print(key)
#skey = str(key,'utf-16')
#print(skey)
#bkey = bytes(skey,'utf-16')
#bkey = bkey[-32:] 
#print(bkey)

for i in range(len(key)):
     print("0x{:02x}".format(key[i]))

key2 = b'0xc2\0xf4\0x1b\0x41\0x0a\0x08\0x64\0x9f\0x50\0xb2\0x1c\0x0f\0xea\0x54\0xa1\0x19\0xde\0x02\0xc8\0x42\0xbf\0x61\0x4a\0x1d\0xaa\0x0f\0x13\0x8e\0xfc\0x1c\0x74\0x6e'

# verify
kdf = PBKDF2HMAC(
     algorithm=hashes.SHA256(),
     length=32,
     salt=salt,
     iterations=100000,
     backend=backend
)
try:
    kdf.verify(b"164e417kuncoro", key2)
    print("success")
except:
    print("key doesn't match")



