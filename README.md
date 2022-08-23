# REAL TIME CHAT MESSENGER

---

This chat application is based on server sent event. This implements Mercure protocol to make this possible.\

## Introduction

### Mercure documentation
Check documentation on [Mercure documentation](https://mercure.rocks/).

## 1 Entities relationship

```mermaid
classDiagram

class Conversation {
    - int id
    - lastMessage
    - messages
    - participants    
}

class Message {
    - int id
    - string content
    - Datetime createdAt
}

class Participant {
    - int id
}

class User {
    - int id
    - string username
    - string email
    - string password
    - array roles
}

Conversation "1..*" --> Message 
Conversation "1..*" --> Participant
User "1..*" --> Participant
User "1..*" --> Message



```