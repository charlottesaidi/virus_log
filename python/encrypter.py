from cryptography.fernet import Fernet
import os

class KeyGenerator:
    # def getKey(self):
    #     with open("filekey.key", "rb") as filekey:
    #         return filekey.read()
        
    def generateKey(self):
        # key = self.getKey()
        # if key == None:
        return Fernet.generate_key()
            # encryptionKey = Fernet.generate_key()
            # with open("filekey.key", "wb") as filekey:
                # filekey.write(encryptionKey)
        # else:
            # return key

class Encrypter:
    _fernet = ""
    _key = ""

    def __init__(self, key):
        self._key = key
        self._fernet = Fernet(self._key)
    
    def encrypt(self, path,*excludeExtensions): 
        i = 0
        for root, dirs, files in os.walk(path):
            for entry in files:
                if not entry.endswith(excludeExtensions):
                    with open(os.path.join(root, entry), "rb") as file:
                        original = file.read()
                    encrypted = self._fernet.encrypt(original)
                    with open(os.path.join(root, entry), "wb") as encrypted_file:
                        encrypted_file.write(encrypted)
                i += 1
        print(str(i) + " fichiers encrypté.s dans le dossier : " + path)

    def decrypt(self, path, excludeExtensions):
        i = 0
        for root, dirs, files in os.walk(path):
            for entry in files:
                if not entry.endswith(excludeExtensions):
                    with open(os.path.join(root, entry), "rb") as _file:
                        encrypted = _file.read()
                    decrypted = self._fernet.decrypt(encrypted)
                    with open(os.path.join(root, entry), "wb") as decrypted_file:
                        decrypted_file.write(decrypted)
                i += 1
        print(str(i) + " fichiers décrypté.s dans le dossier : " + path)