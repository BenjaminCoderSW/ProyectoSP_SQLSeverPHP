USE AdminDB;
GO

CREATE OR ALTER PROCEDURE [dbo].[sp_CrearLoginUsuario]
(
    @LoginName     NVARCHAR(50),
    @UserName      NVARCHAR(50),
    @DatabaseName  NVARCHAR(50),
    @Password      NVARCHAR(100),  -- Contraseña del login
    @RoleName      NVARCHAR(50) = NULL,   -- Rol opcional (ej. db_datareader)
    @SchemaName    NVARCHAR(50) = 'dbo',  -- Esquema opcional (por defecto dbo)
    @TableName     NVARCHAR(50) = NULL,   -- Tabla opcional
    @Permission    NVARCHAR(50) = NULL    -- Permiso opcional (ej. SELECT, INSERT)
)
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        -- 1. Validación: Verificar si el login ya existe en el servidor
        IF EXISTS (SELECT 1 FROM sys.server_principals WHERE name = @LoginName)
        BEGIN
            RAISERROR('El login ya existe.', 16, 1);
            RETURN;
        END;

        -- 2. Crear el login con la contraseña especificada
        DECLARE @SQL NVARCHAR(MAX);
        SET @SQL = 'CREATE LOGIN ' + QUOTENAME(@LoginName) 
                   + ' WITH PASSWORD = ''' + @Password + ''';';
        PRINT 'SQL para crear login: ' + @SQL;  -- Para depuración
        EXEC sp_executesql @SQL;

        -- 3. Crear el usuario en la base de datos indicada
        SET @SQL = 'USE ' + QUOTENAME(@DatabaseName) + ';
                    CREATE USER ' + QUOTENAME(@UserName) 
                  + ' FOR LOGIN ' + QUOTENAME(@LoginName) + ';';
        PRINT 'SQL para crear usuario: ' + @SQL;  -- Para depuración
        EXEC sp_executesql @SQL;

        -- 4. Asignar el usuario a un rol, si se especifica
        IF @RoleName IS NOT NULL
        BEGIN
            SET @SQL = 'USE ' + QUOTENAME(@DatabaseName) + ';
                        EXEC sp_addrolemember ' + QUOTENAME(@RoleName,'''') 
                      + ', ' + QUOTENAME(@UserName) + ';';
            PRINT 'SQL para asignar rol: ' + @SQL;
            EXEC sp_executesql @SQL;
        END;

        -- 5. Conceder permiso sobre una tabla, si se especifica
        IF (@TableName IS NOT NULL AND @Permission IS NOT NULL)
        BEGIN
            SET @SQL = 'USE ' + QUOTENAME(@DatabaseName) + ';
                        GRANT ' + @Permission + ' ON OBJECT::' 
                        + QUOTENAME(@SchemaName) + '.' + QUOTENAME(@TableName)
                        + ' TO ' + QUOTENAME(@UserName) + ';';
            PRINT 'SQL para conceder permiso: ' + @SQL;
            EXEC sp_executesql @SQL;
        END;

        PRINT 'Login y usuario creados correctamente.';
    END TRY
    BEGIN CATCH
        PRINT 'Error: ' + ERROR_MESSAGE();
    END CATCH
END;
GO


-- Ejemplo de prueba:
-- Asegúrate de que la base de datos 'MiBaseDeDatos2' exista y, si vas a dar permisos,
-- que la tabla [dbo].[Clientes] exista en esa BD.

EXEC [dbo].[sp_CrearLoginUsuario]
    @LoginName = 'PruebaLogin4',
    @UserName = 'PruebaUser4',
    @DatabaseName = 'MiBaseDeDatos6',
    @Password = 'Password123!',
    @RoleName = 'db_datareader', 
    @SchemaName = 'dbo',
    @TableName = 'Clientes',
    @Permission = 'SELECT';

-- Listar los logins en el servidor
SELECT name 
FROM sys.server_principals 
WHERE type_desc = 'SQL_LOGIN'
ORDER BY name;

-- Listar los usuarios en la base de datos MiBaseDeDatos6
USE MiBaseDeDatos6;
GO
SELECT name 
FROM sys.database_principals 
WHERE type_desc = 'SQL_USER'
ORDER BY name;

-- Verificar el rol
USE MiBaseDeDatos6;
GO
sp_helprolemember 'db_datareader';





