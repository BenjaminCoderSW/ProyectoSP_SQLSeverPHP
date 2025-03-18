USE AdminDB;
GO

-- Permitir mostrar opciones avanzadas
EXEC sp_configure 'show advanced options', 1;
RECONFIGURE;

-- Habilitar xp_cmdshell
EXEC sp_configure 'xp_cmdshell', 1;
RECONFIGURE;


CREATE OR ALTER PROCEDURE dbo.sp_BackupDatabase
    @DatabaseName NVARCHAR(128),
    @BackupType NVARCHAR(20)  -- Valores permitidos: 'FULL', 'DIFFERENTIAL', 'LOG'
AS
BEGIN
    SET NOCOUNT ON;

    -- Validar que la base de datos exista
    IF NOT EXISTS (SELECT 1 FROM sys.databases WHERE name = @DatabaseName)
    BEGIN
        RAISERROR('La base de datos %s no existe.', 16, 1, @DatabaseName);
        RETURN;
    END;

    -------------------------------------------------------------------
    -- 1. Definir la ruta raíz de backups y determinar subcarpeta y extensión
    -------------------------------------------------------------------
    -- Ajusta esta ruta según donde quieras almacenar los backups en tu máquina física
    DECLARE @BackupRoot NVARCHAR(260) = 'C:\Backups';

    DECLARE @SubFolder NVARCHAR(50);
    DECLARE @Extension NVARCHAR(10);

    IF UPPER(@BackupType) = 'FULL'
    BEGIN
        SET @SubFolder = 'Full';
        SET @Extension = '.bak';
    END
    ELSE IF UPPER(@BackupType) = 'DIFFERENTIAL'
    BEGIN
        SET @SubFolder = 'Differential';
        SET @Extension = '.bak';
    END
    ELSE IF UPPER(@BackupType) = 'LOG'
    BEGIN
        SET @SubFolder = 'Log';
        SET @Extension = '.trn';
    END
    ELSE
    BEGIN
        RAISERROR('Tipo de backup inválido. Use FULL, DIFFERENTIAL o LOG.', 16, 1);
        RETURN;
    END;

    -- Construir la ruta completa: \Backups\<DatabaseName>\<SubFolder>
    DECLARE @FolderPath NVARCHAR(260) = @BackupRoot + '\' + @DatabaseName + '\' + @SubFolder;

    -- Crear la carpeta si no existe (usando xp_cmdshell)
    DECLARE @Cmd NVARCHAR(500) = 'if not exist "' + @FolderPath + '" mkdir "' + @FolderPath + '"';
    EXEC xp_cmdshell @Cmd, NO_OUTPUT;

    -------------------------------------------------------------------
    -- 2. Generar nombre de archivo único con timestamp
    -------------------------------------------------------------------
    DECLARE @TimeStamp NVARCHAR(30) = REPLACE(CONVERT(VARCHAR(19), GETDATE(), 120), ':', '-');
    DECLARE @BackupFile NVARCHAR(300) = @FolderPath + '\' + @DatabaseName + '_' + @SubFolder + '_' + @TimeStamp + @Extension;

    -------------------------------------------------------------------
    -- 3. Construir y ejecutar el comando de backup según el tipo
    -------------------------------------------------------------------
    DECLARE @SQL NVARCHAR(MAX);

    IF UPPER(@BackupType) = 'FULL'
    BEGIN
        SET @SQL = 'BACKUP DATABASE [' + @DatabaseName + '] TO DISK = N''' + @BackupFile + ''' WITH INIT, NAME = N''Full Backup of ' + @DatabaseName + '''';
    END
    ELSE IF UPPER(@BackupType) = 'DIFFERENTIAL'
    BEGIN
        SET @SQL = 'BACKUP DATABASE [' + @DatabaseName + '] TO DISK = N''' + @BackupFile + ''' WITH DIFFERENTIAL, INIT, NAME = N''Differential Backup of ' + @DatabaseName + '''';
    END
    ELSE IF UPPER(@BackupType) = 'LOG'
    BEGIN
        SET @SQL = 'BACKUP LOG [' + @DatabaseName + '] TO DISK = N''' + @BackupFile + ''' WITH INIT, NAME = N''Log Backup of ' + @DatabaseName + '''';
    END

    BEGIN TRY
        EXEC sp_executesql @SQL;
        PRINT 'Backup ejecutado correctamente: ' + @BackupFile;
    END TRY
    BEGIN CATCH
        PRINT 'Error durante el backup: ' + ERROR_MESSAGE();
    END CATCH
END;
GO

-- PRUEBA
BACKUP DATABASE [MiBaseDeDatos6]
TO DISK = 'C:\Backups\MiBaseDeDatos6\Full\PruebaManual.bak'
WITH INIT;

EXEC AdminDB.dbo.sp_BackupDatabase 
    @DatabaseName = 'MiBaseDeDatos6',
    @BackupType = 'FULL';

USE master;
GO
ALTER DATABASE AdminDB SET TRUSTWORTHY ON;
GO

EXEC xp_cmdshell 'dir "C:\Backups\MiBaseDeDatos6\Full"';


