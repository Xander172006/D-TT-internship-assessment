## Test the local development environment
1. Navigate to the project using a browser: `http://localhost/<project_folder>` (example: [http://localhost/web_backend_test_catering_api](http://localhost/web_backend_test_catering_api)). The page should print `Hello World!`.
2. Import the included Postman collection in the Postman application.
3. Set the Postman collection variable `baseUrl` to your correct base URL.
4. Run the Test API call within Postman. It should return `Hello World!`, same as in step 1 with the browser.

### Routing
The base setup uses an external [Router Plugin](https://github.com/bramus/router). The routes are registered in `/routes/routes.php`.

- **Note**: All API routes are prefixed with `/api/` as set by the base path (`$router->setBasePath('/api');`).

To register a route, provide:
1. the path. Example: `/auth/login`
2. the controller and method. Example: `App\Controllers\AuthController@login`

### Database
The database is registered in the DI container. Among other database features, querying the database within a DiAware context (such as a controller) can be done by using `$this->db->executeQuery($query, $bind);`.

This will invoke the executeQuery method of the `App\Plugins\Db\Db` class.

Make sure to import the `dump.sql` file to create the database and tables before testing the API.

---

## API Features

### 1. Facility Management
- **Description**: Manage facilities by adding, updating, and retrieving facility data.
- **Endpoints**:
  - `GET /api/facility` – Retrieve a list of all facilities.
  - `POST /api/facility/create` – Add a new facility.
  - `PUT /api/facility/update/{id}` – Update facility information for a specific ID.

### 2. Location Services
- **Description**: Handle location information.
- **Endpoints**:
  - `GET /api/location` – Retrieve all locations.

---

## Postman API Documentation

The API Postman collection is included in the `/docs/` folder as `D-TT assessment.postman_collection.json`.

To use the Postman collection:
1. Open Postman.
2. Go to **File** > **Import**.
3. Select the file `D-TT assessment.postman_collection.json` from the `/docs/` folder.
4. The API requests will be imported, and you can use them to test the endpoints.

Ensure that the `baseUrl` variable in Postman is set to your local URL (e.g., `http://localhost/api`).

---
