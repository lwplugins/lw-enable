# LW Site Manager - Enable Abilities

LW Enable registers 2 abilities with LW Site Manager, allowing AI agents and automation tools to read and toggle WordPress feature settings programmatically.

These abilities are only active when LW Site Manager is also installed and activated. No hard dependency - the integration is a no-op otherwise.

## Abilities

| Ability | Type | Description |
|---------|------|-------------|
| `lw-enable/get-options` | readonly | Get all LW Enable feature toggle settings |
| `lw-enable/set-options` | write | Toggle features on or off |

## Authentication

All requests require a WordPress Application Password:

```bash
curl -u "user@example.com:XXXX XXXX XXXX XXXX XXXX XXXX" <URL>
```

## lw-enable/get-options

Retrieve the current state of all LW Enable feature toggles.

**Method:** GET

```bash
curl -u "user:app-password" \
  "https://example.com/wp-json/wp-abilities/v1/abilities/lw-enable/get-options/run"
```

**Response:**
```json
{
  "success": true,
  "options": {
    "svg": true
  }
}
```

**Available fields:**

| Field | Type | Description |
|-------|------|-------------|
| `svg` | bool | Whether SVG uploads are enabled |

## lw-enable/set-options

Toggle one or more LW Enable features. Only the provided keys are updated; others remain unchanged.

**Method:** POST

```bash
curl -u "user:app-password" \
  -X POST -H "Content-Type: application/json" \
  -d '{
    "input": {
      "options": {
        "svg": true
      }
    }
  }' \
  "https://example.com/wp-json/wp-abilities/v1/abilities/lw-enable/set-options/run"
```

**Response:**
```json
{
  "success": true,
  "message": "1 option(s) updated.",
  "options": {
    "svg": true
  }
}
```

**Disabling SVG uploads:**
```bash
curl -u "user:app-password" \
  -X POST -H "Content-Type: application/json" \
  -d '{"input": {"options": {"svg": false}}}' \
  "https://example.com/wp-json/wp-abilities/v1/abilities/lw-enable/set-options/run"
```

## Permissions

| Ability | Required capability |
|---------|-------------------|
| `lw-enable/get-options` | `manage_options` |
| `lw-enable/set-options` | `manage_options` |
