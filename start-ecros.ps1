param(
    [int]$Port = 8000
)

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$phpPath = Join-Path $projectRoot '..\.tools\php\php.exe'

if (Test-Path $phpPath) {
    $phpCommand = $phpPath
} else {
    $phpCommand = Get-Command php -ErrorAction SilentlyContinue | Select-Object -ExpandProperty Source -First 1
}

if (-not $phpCommand) {
    Write-Error "PHP was not found. Install PHP 8.3+ or make sure php is available on PATH."
    exit 1
}

Set-Location $projectRoot
& $phpCommand artisan migrate:fresh --seed
& $phpCommand artisan serve --host=127.0.0.1 --port=$Port
