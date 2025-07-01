#Keeping the name Fedex_OrderStatus (Reason is, i have my local setup with this, To male it on my development env, i use this name)
# Fedex_OrderStatus Magento 2 Module

## Overview

Fedex_OrderStatus is a custom Magento 2.4.7+ compatible module designed to provide enhanced control over order status management. It supports admin-level status creation, mass actions, status updates via REST API, order tracking logs, customer notifications, and caching for performance.

---

## 📦 Installation Steps

1. **Copy the Module**
   ```bash
   app/code/Fedex/OrderStatus
   ```

2. **Enable and Install the Module**
   ```bash
   php bin/magento module:enable Fedex_OrderStatus
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento cache:flush
   ```

3. **Verify Module Status**
   ```bash
   php bin/magento module:status Fedex_OrderStatus
   ```

---

## ⚙️ Admin Panel Usage

- Go to `Sales > Order Status` in the admin menu.
- You can:
  - View, add, edit, delete statuses.
  - Enable/disable multiple statuses via mass action.

---

## 🔌 REST API

### Endpoint

```
POST /rest/V1/fedex/orderstatus/update
```

### Authentication

Use Bearer Token (Admin or Integration token).

### Request Payload

```json
{
  "increment_id": "100000001",
  "new_status": "custom_approved"
}
```

### Response

```json
{
  "message": "Order status updated successfully."
}
```

---

## 📧 Email Notification

- If an order is marked as `shipped`, an automatic email is triggered to the customer.
- Template: `view/frontend/email/order_status_shipped.html`
- Configurable via Admin > Marketing > Email Templates

---

## 🗄️ Database Tables

### 1. `fedex_order_status`

| Column     | Type     | Description              |
|------------|----------|--------------------------|
| id         | INT      | Primary Key              |
| status     | VARCHAR  | Custom Status Name       |
| is_active  | SMALLINT | 1 = Active, 0 = Inactive |
| created_at | TIMESTAMP| Record created time      |
| updated_at | TIMESTAMP| Record last update time  |

### 2. `fedex_order_status_log`

| Column      | Type     | Description               |
|-------------|----------|---------------------------|
| log_id      | INT      | Primary Key               |
| order_id    | INT      | Magento Order ID          |
| old_status  | VARCHAR  | Previous status           |
| new_status  | VARCHAR  | Updated status            |
| created_at  | TIMESTAMP| Timestamp of change       |

---

## 🚀 Caching

- Implemented via Magento’s Cache API and `IdentityInterface`.

- `getAvailableStatuses()` caches the active statuses using:

  `Fedex\OrderStatus\Model\Status::CACHE_TAG`

- Cache is invalidated when statuses are added, updated, or deleted.

---

## 🔁 Events & Observers

### Observer: `sales_order_save_after`

- Logs old and new order status to `fedex_order_status_log`.

- Triggers email to customer if marked as `shipped`.

---

## ✅ Testing

### Admin

- Go to Sales > Order Status and perform actions (add/edit/delete/status change).

### API

- Use Postman or CURL.

- Use a valid admin token for authentication.

---

## 🧠 Architectural Decisions & Best Practices

- Follows **Magento 2 coding standards** including:

  - PSR-4 autoloading

  - Dependency Injection (DI)

  - Avoidance of direct `ObjectManager` usage

- **Repositories and Service Contracts**:

  - Custom APIs and data layers use repository pattern

  - Avoided raw SQL, using collections and models

- **Declarative Schema**:

  - Used `db_schema.xml` for future-proof table creation and lifecycle

- **Performance Optimization**:

  - Caching of frequently accessed status list

  - Minimal DB load via filtered collections

- **Security**:

  - Authenticated API with Magento's bearer token

  - Validates status against active and allowed states

---

## 📄 Summary

This module provides:

- Custom admin grid and status management

- REST API endpoint for order updates

- Logging and email notification

- Efficient caching and scalable design

- Adheres to Magento 2.4.7 development standards

---

New Updates:- Adding Limiting

- I Used Plugin and Helper to Rate Limit the API Calls

- As for default Magento API Rate Limiting we can apply on web server.

## Unit Tests:-

- Api/OrderStatusManagementTest.php

Verifies updateStatus() updates order status.

Tests exception handling if order not found.

- Model/StatusTest.php

Tests getOrderId() and getStatus() setters/getters.

- Controller/Adminhtml/Status/MassDeleteTest.php

Mocks request filtering and ensures delete() is called on each selected item.

Asserts that success message and redirect are properly returned.

## Integration Tests

- Model/StatusTest.php

Creates and saves a real Status model.

Loads it back from DB and verifies data consistency.

Tests default behavior of new models.

- Controller/Adminhtml/Status/MassDeleteTest.php

Creates real status records.

Simulates an admin backend POST request.

Asserts records are removed from the DB after deletion.


