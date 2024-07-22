# AmoCRM Integration Application

## Описание

AmoCRM Integration Application - это система для интеграции с AmoCRM, предназначенная для управления контактами и сделками. Приложение состоит из двух частей: фронтенда и бэкенда. Фронтенд написан на React, а бэкенд - на PHP с использованием Apache.

## Технологии

- **Frontend**: React, JavaScript, HTML, CSS
- **Backend**: PHP, Apache
- **Docker**: для контейнеризации и развертывания
- **AmoCRM API**: для взаимодействия с CRM

## Установка и запуск

### Требования

- [Docker](https://www.docker.com/products/docker-desktop) (и [Docker Compose](https://docs.docker.com/compose/install/) для удобства) (опционально, можно развернуть самостоятельно, например с помощью OpenServer)
- [Git](https://git-scm.com/) (опционально, для клонирования репозитория)

### Клонирование репозитория

git clone https://github.com/dariaverina/amocrm-integration.git
cd your-repository-folder

## Запуск с помощью Docker

1. **Убедитесь, что Docker и Docker Compose установлены и запущены на вашем компьютере.**

2. **Создайте `.env` файлы для фронтенда и бэкенда.**

3. **Запустите Docker Compose:**
   docker-compose up --build

## Пример использования

### Запуск приложения

После запуска Docker Compose, откройте ваш веб-браузер и перейдите по следующему адресу:

- **Фронтенд:** [http://localhost:3000](http://localhost:3000)
- **Бэкенд:** [http://localhost:8000](http://localhost:8000) (используется для взаимодействия с AmoCRM)

### Пример взаимодействия

- **Фронтенд:** Используйте интерфейс приложения для создания новых контактов и сделок.
- **Бэкенд:** Обрабатывает запросы из фронтенда и взаимодействует с AmoCRM API.

### Ссылка на демонстрационное видео

Посмотрите [демонстрационное видео](https://youtu.be/Sk3NvNvP35Y), чтобы увидеть, как работает приложение.

## 👤 Автор

Разработчик: **[Верина Дарья]**