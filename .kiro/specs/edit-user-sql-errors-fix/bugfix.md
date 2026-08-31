# Bugfix Requirements Document

## Introduction

When editing an existing user of type "Empresa" in `usuariospanel.php`, the save operation fails due to multiple SQL errors. The bugs affect the UPDATE and INSERT queries for the `empresa` table, the `representante` table INSERT/SELECT, and the JOIN used when loading empresa data for editing. These errors prevent any empresa-type user from being created or updated successfully through the admin panel.

## Bug Analysis

### Current Behavior (Defect)

1.1 WHEN an admin attempts to update an existing empresa-type user THEN the system produces a SQL syntax error because the UPDATE query for `empresa` is missing a comma between `Numero_Puerta=?` and `Descripcion=?`

1.2 WHEN an admin attempts to update an existing empresa-type user THEN the system produces a parameter count mismatch error because the execute() array for the UPDATE query passes 12 values while the query only contains 9 placeholders (`$calle`, `$esquina`, `$numpuerta` are duplicated and `Categoria_Empresarial` receives `$calle` instead of a category value)

1.3 WHEN an admin attempts to create a new empresa-type user THEN the system produces a SQL syntax error because the INSERT query for `empresa` uses assignment syntax (`Calle=?, Esquina=?, Numero_Puerta=?`) inside the column list, which is invalid SQL

1.4 WHEN an admin attempts to create a new empresa-type user THEN the system produces a parameter count mismatch error because the INSERT execute() array passes 9 values while the VALUES clause only has 7 placeholders

1.5 WHEN the system looks up or inserts the representante associated with a company THEN the system produces a SQL error because the `representante` table has no `ID_Empresa` column, yet the queries reference `ID_Empresa` in both INSERT and WHERE clauses

1.6 WHEN the system loads empresa data for editing THEN the JOIN query `ON r.ID_Empresa = e.ID_Usuario` references a non-existent `ID_Empresa` column in `representante`, causing the query to fail or return no representative data

1.7 WHEN the system performs a SELECT or UPDATE on the `empresa` table filtering by `ID_Usuario` THEN the query may fail or behave unexpectedly on case-sensitive systems because the schema defines the column as `ID_USUARIO` (all caps) while PHP uses `ID_Usuario` (mixed case)

### Expected Behavior (Correct)

2.1 WHEN an admin updates an existing empresa-type user THEN the system SHALL execute a syntactically valid UPDATE query for `empresa` with a comma separating every column assignment including between `Numero_Puerta=?` and `Descripcion=?`

2.2 WHEN an admin updates an existing empresa-type user THEN the system SHALL execute the UPDATE query with exactly the correct number of bound parameters matching the number of `?` placeholders, with `Categoria_Empresarial` receiving the appropriate `$descripcion` or category value and without duplicating address fields

2.3 WHEN an admin creates a new empresa-type user THEN the system SHALL execute a syntactically valid INSERT query for `empresa` using a plain comma-separated column list (no `=?` syntax) with the correct number of columns matching the number of VALUES placeholders

2.4 WHEN an admin creates a new empresa-type user THEN the system SHALL execute the INSERT query with exactly the correct number of bound parameters matching the number of `?` placeholders in the VALUES clause

2.5 WHEN the system inserts or looks up a representante record THEN the system SHALL use the correct foreign key relationship, inserting `ID_Empresa` as the empresa's `ID_Empresa` primary key (not `ID_Usuario`) and the `representante` table SHALL have an `ID_Empresa` FK column, OR the lookup/insert SHALL use `ID_Representante` stored on the `empresa` row

2.6 WHEN the system loads empresa data for editing THEN the JOIN between `empresa` and `representante` SHALL use `r.ID_Representante = e.ID_Representante`, which are the actual matching columns defined in the schema

2.7 WHEN the system queries or updates the `empresa` table by user identifier THEN the system SHALL use the exact column name `ID_USUARIO` as defined in the database schema, ensuring compatibility across all systems regardless of case-sensitivity settings

### Unchanged Behavior (Regression Prevention)

3.1 WHEN an admin creates or updates a Persona-type user THEN the system SHALL CONTINUE TO save the user record and persona entry correctly without being affected by the empresa query changes

3.2 WHEN an admin edits basic user fields (email, nombre, teléfono, rol, contraseña) for any user type THEN the system SHALL CONTINUE TO update the `usuario` table correctly

3.3 WHEN an admin deletes a user THEN the system SHALL CONTINUE TO remove the user record and cascade deletions as before

3.4 WHEN the user list is displayed THEN the system SHALL CONTINUE TO show all users with their roles fetched via the LEFT JOIN with `rol`

3.5 WHEN a Persona-type user is edited and saved THEN the system SHALL CONTINUE TO ensure a `persona` row exists for that user without errors

3.6 WHEN an empresa-type user is switched to Persona type during an update THEN the system SHALL CONTINUE TO delete the empresa and representante records and create a persona record as intended

---

## Bug Condition Summary

**Bug Condition Function** — identifies inputs that trigger the bugs:

```pascal
FUNCTION isBugCondition(X)
  INPUT: X of type UserFormSubmission
  OUTPUT: boolean

  RETURN X.tipo_persona = "Empresa"
         AND (X.action = "insert" OR X.action = "update")
END FUNCTION
```

**Property: Fix Checking**
```pascal
FOR ALL X WHERE isBugCondition(X) DO
  result ← saveUser'(X)
  ASSERT no_sql_error(result)
  AND empresa_record_persisted(result)
  AND representante_record_persisted(result)
END FOR
```

**Property: Preservation Checking**
```pascal
FOR ALL X WHERE NOT isBugCondition(X) DO
  ASSERT saveUser(X) = saveUser'(X)
END FOR
```
