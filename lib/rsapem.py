#coding:utf8
# pupulate-pub-key-v3.py
#
from cryptography.hazmat.backends import default_backend
from cryptography.hazmat.primitives.asymmetric import rsa
from cryptography.hazmat.primitives import serialization
pub_key="D28B9DAEBBBC2884F31981791EF959AC0AB1BB1987ADDE98EA6932CB0AB5DCFE592284D296F3A0FDB8962496597F4BF1142972F08E9982164896ADBAA05284EA56072A1E74D8D134570386466C36AEA4FFAB6BC2C1B911A1F1ADC5EF89BB1AA07EC14F540DD49C2EC3CA95C5D290E7C2ED418CA469F13C3AE69B9D06BE6B495D"

# 从little-endian格式的数据缓冲data中解析公钥模数并构建公钥
def populate_public_key(data):
    # convert bytes to integer with int.from_bytes
    # 指定从little格式将bytes转换为int，一句话就得到了公钥模数，省了多少事
    n = int(data,16)
    e = 65537

    # 使用(e, n)初始化RSAPublicNumbers，并通过public_key方法得到公钥
    # construct key with parameter (e, n)
    key = rsa.RSAPublicNumbers(e, n).public_key(default_backend())
    return key


# 将公钥以PEM格式保存到文件中
def save_pub_key(pub_key, pem_name):
    # 将公钥编码为PEM格式的数据
    pem = pub_key.public_bytes(
        encoding=serialization.Encoding.PEM,
        format=serialization.PublicFormat.SubjectPublicKeyInfo
    )

    print(pem)

    # 将PEM个数的数据写入文本文件中
    with open(pem_name, 'w+') as f:
        f.writelines(pem.decode())

    return

if __name__ == '__main__':
        pub_key = populate_public_key(data=pub_key)
        pem_file = r'pub_key.pem'
        save_pub_key(pub_key, pem_file)
