$mdPath = "C:\xampp\htdocs\hotel\01_SCHEMA_BASE_DATOS_COMPLETO.md"
$sqlPath = "C:\xampp\htdocs\hotel\sql\schema.sql"

$md = Get-Content -Path $mdPath -Raw -Encoding UTF8
$matches = [regex]::Matches($md, '(?s)```sql(.*?)```')

$outArray = @(
    "CREATE DATABASE IF NOT EXISTS hotelcore2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;",
    "USE hotelcore2;",
    "SET FOREIGN_KEY_CHECKS=0;"
)

foreach ($m in $matches) {
    if (-not [string]::IsNullOrWhiteSpace($m.Groups[1].Value)) {
        $outArray += $m.Groups[1].Value.Trim()
    }
}

$outArray += "SET FOREIGN_KEY_CHECKS=1;"

$finalSql = $outArray -join "`r`n`r`n"
Set-Content -Path $sqlPath -Value $finalSql -Encoding UTF8
Write-Output "Extraccion de SQL completada con exito hacia $sqlPath"
