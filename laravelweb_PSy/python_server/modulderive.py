#import dependencies
import os
import base64
from cryptography.hazmat.primitives import hashes
from cryptography.hazmat.primitives.kdf.pbkdf2 import PBKDF2HMAC
from cryptography.hazmat.backends import default_backend

# string = "Python is interesting."

# # string with encoding 'utf-8'
# arr = bytes(string, 'utf-8')
# print(arr)

#make a key
backend = default_backend()
# Salts should be randomly generated
##! code 1 adalah masterkey dari database setiap user ##
def enkrip(data,masterKey):
    #global salt,key
    #kode1= str(code1)
    #kode1= "3105201908:00shift3"
    salt = bytes(masterKey)
    #salt = word1 #<tanggal ddmmyyyy><hh:mm>shift<no3>
    #salt = os.urandom(16)
    
    # derive
    kdf = PBKDF2HMAC(
         algorithm=hashes.SHA256(),
         length=32,
         salt=salt,
         iterations=100000,
         backend=backend
    )
    #kode2=str(code2)
    #kode2="164e417kuncoro"
    uniqueKey = kdf.derive(bytes(data))
    encUniqueKey = base64.b64encode(uniqueKey)
    #key = kdf.derive(b"164e417kuncoro") #<nomor induk satpam><nama satpam>
    #print(key)
    #skey = str(key,'utf-16')
    #print(skey)
    #bkey = bytes(skey,'utf-16')
    #bkey = bkey[-32:] 
    #print(bkey)

    #for i in range(len(key)):
    #     print("0x{:02x}".format(key[i]))
    return encUniqueKey

def dekrip(dataQR,shift,masterKey):
    salt = bytes(masterKey)
    ####*key success###########
    #?key2 = b'\xc2\xf4\x1bA\n\x08d\x9fP\xb2\x1c\x0f\xeaT\xa1\x19\xde\x02\xc8B\xbfaJ\x1d\xaa\x0f\x13\x8e\xfc\x1ctn'
    ####*key doesn't match#####
    #?key2 = b'0xc2\0xf4\0x1b\0x41\0x0a\0x08\0x64\0x9f\0x50\0xb2\0x1c\0x0f\0xea\0x54\0xa1\0x19\0xde\0x02\0xc8\0x42\0xbf\0x61\0x4a\0x1d\0xaa\0x0f\0x13\0x8e\0xfc\0x1c\0x74\0x6e'

    kdf = PBKDF2HMAC(
        algorithm=hashes.SHA256(),
        length=32,
        salt=salt,
        iterations=100000,
        backend=backend
    )

    try:
        qrcode = base64.b64decode(dataQR)
        kdf.verify(bytes(shift),qrcode)
        print("success")
    except:
        print("key doesn't match")


    # verify
    #kdf = PBKDF2HMAC(
    #    algorithm=hashes.SHA256(),
    #    length=32,
    #     salt=salt,
    #     iterations=100000,
    #     backend=backend
    #)
    #if key2==key:
    #    print ("||\t\t    success",end='')
    #    print("\t\t\t||")
    #else:
    #    print ("||\t\tdoesn't match !",end="")
    #    print("\t\t\t||")
    
    # try:
    #     kdf.verify(key, key2)
    #     print("success")
    # except:
    #     print("key doesn't match")


# c1="3105201908:00shift3"
# c2="164e417kuncoro"

# enkrip(c1,c2)
# print(key)
# dekrip()
# print(key2)

