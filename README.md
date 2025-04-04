# Cyph.ro encrypted message sending

What is this tool?

- A test implementation of secure message sending and response receival with symmetric and asymmetric keys
- All data stored on the server is encrypted
- Data is only available and visible between the sender and receiver

How does it work?

- A sender writes a message. It gets saved to the server, encrypted with a symmetric key. The symmetric key is stored encrypted on the server, encrypted with an asymmetric keypair. The public key is stored on the server.
- The application generates two links, one for responders to send responses, and one for the sender to see responses

- The responder link contains the message id and the symmetric key needed to decrypt the message. The responder therefore sees the message id, grabs the encrypted message and decrypts it, and grabs the public key. It can write as many responses as it wants and encrypts them with the public key.

- The host link contains the asymmetric private key, and the message id. The host grabs the encrypted message, the public key and the encrypted symmetric key. It uses the asymmetric keypair to decrypt the symmetric key so as to decrypt the message and see its own message once again, and grabs all the responses to the messages and decrypts them with the asymmetric keypair.

## Why was this tool created?

- This tool was created as a preparation for a fully encrypted calendar sharing application, to prepare the groundwork.
