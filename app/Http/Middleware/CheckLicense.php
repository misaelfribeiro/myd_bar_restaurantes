<?php
namespace App\Http\Middleware;
use App\Models\License;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class CheckLicense
{
 public function handle(Request $request, Closure $next)
 {
 if ($request->is('admin/license
 private function verifyLicense()
 {
 $hardwareId = $this->getHardwareId();
 $license = License::where('hardware_id', $hardwareId)
 ->where('status', 'ativa')
 ->first();
 if (!$license) {
 $license = License::whereNull('hardware_id')
 ->where('status', 'ativa')
 ->first();
 if ($license) {
 $license->ativar($hardwareId);
 return true;
 }
 return false;
 }
 return $license->isAtiva();
 }
 private function getHardwareId()
 {
 if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
 $output = shell_exec('wmic csproduct get uuid');
 if ($output) {
 $lines = explode("\n", trim($output));
 if (isset($lines[1])) {
 return trim($lines[1]);
 }
 }
 }
 if (file_exists('/etc/machine-id')) {
 return trim(file_get_contents('/etc/machine-id'));
 }
 if (file_exists('/var/lib/dbus/machine-id')) {
 return trim(file_get_contents('/var/lib/dbus/machine-id'));
 }
 $output = shell_exec('getmac');
 if ($output) {
 preg_match('/([0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2}-[0-9A-F]{2})/i', $output, $matches);
 if (isset($matches[1])) {
 return str_replace('-', '', $matches[1]);
 }
 }
 return gethostname();
 }
}