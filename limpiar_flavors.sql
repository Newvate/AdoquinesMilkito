DELETE f1 FROM flavors f1
INNER JOIN flavors f2
  ON f1.name = f2.name
 AND f1.id > f2.id;

ALTER TABLE flavors
CONVERT TO CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

ALTER TABLE flavors
MODIFY emoji VARCHAR(8)
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci
NOT NULL;

UPDATE flavors SET emoji='🍨' WHERE name='Lúcuma';
UPDATE flavors SET emoji='🍫' WHERE name='Chocolate';
UPDATE flavors SET emoji='🍓' WHERE name='Fresa';
UPDATE flavors SET emoji='🥭' WHERE name='Maracuyá';
UPDATE flavors SET emoji='🍦' WHERE name='Vainilla';
UPDATE flavors SET emoji='🌿' WHERE name='Menta choco';
