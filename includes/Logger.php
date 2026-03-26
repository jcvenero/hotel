<?php
/**
 * Clase Logger para registro de eventos y auditoría del sistema de hoteles.
 */

class Logger {
    private static $logFile = __DIR__ . '/../logs/errors.log';
    private static $auditFile = __DIR__ . '/../logs/audit.log';

    public static function error($message, $context = []) {
        self::write(self::$logFile, 'ERROR', $message, $context);
    }

    public static function info($message, $context = []) {
        self::write(self::$logFile, 'INFO', $message, $context);
    }

    public static function audit($userId, $action, $entity, $entityId, $changes = []) {
        $context = [
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'changes' => json_encode($changes),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];
        self::write(self::$auditFile, 'AUDIT', "Audit log record", $context);
        
        // Guardar también en tabla de la BD (Requerimiento de Fase 1)
        try {
            require_once __DIR__ . '/Database.php';
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO event_logs (usuario_id, accion, entidad, entidad_id, cambios, ip) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $action,
                $entity,
                $entityId,
                $context['changes'],
                $context['ip']
            ]);
        } catch (Exception $e) {
            self::write(self::$logFile, 'ERROR', "Failed to write audit to DB: " . $e->getMessage());
        }
    }

    private static function write($file, $level, $message, $context = []) {
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        $date = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' | Context: ' . json_encode($context) : '';
        $logEntry = "[$date] [$level] $message$contextString" . PHP_EOL;
        file_put_contents($file, $logEntry, FILE_APPEND);
    }
}
?>
