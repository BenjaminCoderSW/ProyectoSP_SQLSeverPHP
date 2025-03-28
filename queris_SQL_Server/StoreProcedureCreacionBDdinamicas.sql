USE AdminDB;
GO

CREATE OR ALTER PROCEDURE [dbo].[sp_CrearBaseDeDatos]
    @DBName NVARCHAR(128),
    @DataFilePath NVARCHAR(260),
    @LogFilePath NVARCHAR(260),
    @DataFileSizeMB INT = 50,
    @LogFileSizeMB INT = 25,
    @DataFileGrowth NVARCHAR(10) = '25%',
    @LogFileGrowth NVARCHAR(10) = '25%',
    @DataFileMaxSize INT = 400,
    -- Parámetros opcionales para un filegroup secundario
    @SecFGName NVARCHAR(128) = NULL,        -- Nombre del filegroup secundario
    @SecDataFilePath NVARCHAR(260) = NULL,  -- Ruta del archivo de datos para el filegroup secundario
    @SecDataFileSizeMB INT = 50,
    @SecDataFileGrowth NVARCHAR(10) = '25%',
    @SecDataFileMaxSize INT = 400
AS
BEGIN
    SET NOCOUNT ON;

    -- Verificar si la base de datos ya existe
    IF EXISTS (SELECT 1 FROM sys.databases WHERE name = @DBName)
    BEGIN
        PRINT 'La base de datos ya existe.';
        RETURN -20;
    END;

    DECLARE @SQL NVARCHAR(MAX);

    /*
      Bloque principal: filegroup PRIMARY.
      Mantenemos tu estructura original, pero sin LOG ON todavía.
    */
    SET @SQL = N'CREATE DATABASE ' + QUOTENAME(@DBName) + N'
    ON PRIMARY 
    (
        NAME = ' + QUOTENAME(@DBName + '_Data') + N',
        FILENAME = ''' + @DataFilePath + N''',
        SIZE = ' + CAST(@DataFileSizeMB AS NVARCHAR(10)) + N'MB,
        FILEGROWTH = ' + @DataFileGrowth + 
        CASE 
            WHEN @DataFileMaxSize IS NOT NULL 
            THEN N', MAXSIZE = ' + CAST(@DataFileMaxSize AS NVARCHAR(10)) + N'MB'
            ELSE N''
        END + N'
    )';

    /*
      Si se especifica un filegroup secundario y su archivo,
      se añade un segundo bloque con la sintaxis FILEGROUP [Nombre].
    */
    IF (@SecFGName IS NOT NULL AND @SecDataFilePath IS NOT NULL)
    BEGIN
        SET @SQL += N',
    FILEGROUP ' + QUOTENAME(@SecFGName) + N'
    (
        NAME = ' + QUOTENAME(@DBName + '_' + @SecFGName) + N',
        FILENAME = ''' + @SecDataFilePath + N''',
        SIZE = ' + CAST(@SecDataFileSizeMB AS NVARCHAR(10)) + N'MB,
        FILEGROWTH = ' + @SecDataFileGrowth +
        CASE 
            WHEN @SecDataFileMaxSize IS NOT NULL 
            THEN N', MAXSIZE = ' + CAST(@SecDataFileMaxSize AS NVARCHAR(10)) + N'MB'
            ELSE N''
        END + N'
    )';
    END;

    -- Ahora añadimos el bloque LOG ON
    SET @SQL += N'
    LOG ON 
    (
        NAME = ' + QUOTENAME(@DBName + '_Log') + N',
        FILENAME = ''' + @LogFilePath + N''',
        SIZE = ' + CAST(@LogFileSizeMB AS NVARCHAR(10)) + N'MB,
        FILEGROWTH = ' + @LogFileGrowth + N'
    );';

    BEGIN TRY
        -- PRINT @SQL;  -- Descomenta para depurar
        EXEC sp_executesql @SQL;
        RETURN 0; -- Éxito
    END TRY
    BEGIN CATCH
        PRINT ERROR_MESSAGE();
        RETURN -2; -- Error
    END CATCH
END;
GO


-- Ejemplo de prueba:
EXEC [dbo].[sp_CrearBaseDeDatos] 
    @DBName = 'MiBaseDeDatos5',
    @DataFilePath = 'C:\SQLData\MiBaseDeDatos5.mdf',
    @LogFilePath = 'C:\SQLData\MiBaseDeDatos5.ldf',
    @DataFileSizeMB = 50,
    @LogFileSizeMB = 10,
    @DataFileGrowth = '10%',
    @LogFileGrowth = '10%',
    @DataFileMaxSize = 500,
    @SecFGName = 'Secundario', 
    @SecDataFilePath = 'C:\SQLData\MiBaseDeDatos5_Sec.ndf',
    @SecDataFileSizeMB = 30,
    @SecDataFileGrowth = '15%',
    @SecDataFileMaxSize = 300;

-- Verificar la creación:
SELECT name 
FROM sys.databases 
WHERE name = 'MiBaseDeDatos5';


