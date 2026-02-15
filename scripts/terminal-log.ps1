param(
    [ValidateSet('start', 'stop', 'status', 'watch', 'append', 'run')]
    [string]$Action = 'start',
    [string]$LogPath = "dokumentasi aplikasi/LOG_TERMINAL.txt",
    [int]$Tail = 80,
    [string]$Message = '',
    [string]$Command = ''
)

function Ensure-LogFile {
    $dir = Split-Path -Parent $LogPath
    if (-not [string]::IsNullOrWhiteSpace($dir) -and -not (Test-Path -LiteralPath $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
    if (-not (Test-Path -LiteralPath $LogPath)) {
        New-Item -ItemType File -Path $LogPath -Force | Out-Null
    }
}

if ($Action -eq 'start') {
    Ensure-LogFile
    try {
        Start-Transcript -Path $LogPath -Append -Force | Out-Null
        Write-Host "Terminal logging aktif. File: $LogPath"
        Write-Host "Perintah stop  : .\\scripts\\terminal-log.ps1 -Action stop"
        Write-Host "Perintah watch : .\\scripts\\terminal-log.ps1 -Action watch"
    } catch {
        if ($_.Exception.Message -match 'already transcribing') {
            Write-Host "Transcript sudah aktif di session PowerShell ini."
        } else {
            throw
        }
    }
    return
}

if ($Action -eq 'stop') {
    try {
        Stop-Transcript | Out-Null
        Write-Host "Terminal logging dihentikan."
    } catch {
        Write-Host "Tidak ada transcript aktif di session ini."
    }
    return
}

if ($Action -eq 'status') {
    $isTranscriptLikelyActive = [bool]($global:transcribing -or $Host.PrivateData.TranscriptEnabled)
    Ensure-LogFile
    Write-Host "Log file: $LogPath"
    Write-Host "Ukuran  : $((Get-Item -LiteralPath $LogPath).Length) byte"
    if ($isTranscriptLikelyActive) {
        Write-Host "Status  : kemungkinan aktif di session ini."
    } else {
        Write-Host "Status  : belum terdeteksi aktif di session ini."
    }
    return
}

if ($Action -eq 'watch') {
    Ensure-LogFile
    Write-Host "Watch aktif. Tekan Ctrl + C untuk berhenti."
    Write-Host "Auto-refresh saat command baru masuk ke log."
    Get-Content -LiteralPath $LogPath -Tail $Tail -Wait
    return
}

if ($Action -eq 'append') {
    Ensure-LogFile
    if ([string]::IsNullOrWhiteSpace($Message)) {
        Write-Host "Message kosong. Gunakan -Message untuk isi log."
        return
    }
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -LiteralPath $LogPath -Value "[$timestamp] [CODEX] $Message"
    Write-Host "Catatan Codex ditulis ke log."
    return
}

if ($Action -eq 'run') {
    Ensure-LogFile
    if ([string]::IsNullOrWhiteSpace($Command)) {
        Write-Host "Command kosong. Gunakan -Command untuk perintah yang ingin dijalankan."
        return
    }

    $timestampStart = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -LiteralPath $LogPath -Value "[$timestampStart] [CODEX-CMD] $Command"
    Add-Content -LiteralPath $LogPath -Value "[$timestampStart] [CODEX-OUT-BEGIN]"

    $outputLines = @()
    & {
        Invoke-Expression $Command 2>&1 | ForEach-Object {
            $line = $_.ToString()
            $outputLines += $line
            Write-Host $line
        }
    }

    $timestampEnd = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    if ($outputLines.Count -gt 0) {
        Add-Content -LiteralPath $LogPath -Value $outputLines
    }
    Add-Content -LiteralPath $LogPath -Value "[$timestampEnd] [CODEX-OUT-END] exit_code=$LASTEXITCODE success=$?"
    return
}
