# Installation Guide

1. **Start Docker containers:**
   ```sh
   docker-compose up -d
   ```

2. **Enter the PHP container:**
   ```sh
   docker-compose exec php bash
   ```

3. **Go to the application directory:**
   ```sh
   cd app
   ```

4. **Create the `.env` file**  
   Copy the contents from the example or create a new `.env` file with the following variables:
   ```
   APP_ENV=dev
   APP_SECRET=your_secret
   DATABASE_URL=mysql://symfony:symfony@mysql:3306/symfony
   MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
   MAILER_DSN=null://null
   ```

5. **Install dependencies:**
   ```sh
   composer install
   ```

6. **Run database migrations:**
   ```sh
   php bin/console doctrine:migrations:migrate
   ```

7. **Load sample data (optional):**
   ```sh
   php bin/console doctrine:fixtures:load
   ```