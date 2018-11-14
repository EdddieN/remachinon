<?php

namespace Remachinon\Http\Controllers;

use Remachinon\Models\Device;
use Remachinon\Models\DeviceTunnel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Bluerhinos\phpMQTT;

class DeviceTunnelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Opens the tunnel with the remote device
     *
     * @param DeviceTunnel $tunnel
     * @return \Illuminate\Http\Response
     */
    public function connect($id)
    {
        $device_tunnel = DeviceTunnel::find($id);
        $this->authorize('connect', $device_tunnel);

        // We need requesting user's API access token to send to Machinon and perform auto login.

        // Comprueba que el tunel ya este abierto y simplemente recargar la pagina
        // It may happen that somebody opens the tunnel while you already have the listing page loaded with the
        // "open tunnel" icon. So if you click "open tunnel" the tunnel may have been already opened....
        if ($device_tunnel->is_enabled &&
            Carbon::parse($device_tunnel->updated_at)->addHours(2)->greaterThan(Carbon::now())) {
                $retry = 1;
                do {
                    $fp = @fsockopen("127.0.0.1", $device_tunnel->port, $errno, $errstr, 30);
                    if ($fp) {
                        // Updating the updated_at field by just saving
                        $device_tunnel->save();
                        return true; // Show modalbox with link to tunnel or javascript to direct open
                    } else {
                        sleep (2);
                        $retry++;
                    }
                } while ($retry <= 5); // Try connecting up to 5 times
        }

        // comprobar puerto disponible, elegido al azar pero cada puerto se testeara una sola vez
        $tunnel_ports = array(10001, 30000);
        $port_found = false;
        $tryout_counter = 0;
/////// FOR DEBUG ON MAC USE THIS DEMO LIST
        $used_ports = array(48000,48001,199,48007,48008,48009,3306,49197,8400,59410,8402,22,47768,25,1311,80,22,25,443);
/////// COMMENT THIS ON MACS- NOT WORK THE SAME
        // exec("netstat -lnt | awk '{print $4}' | sed -e 's/.*://'", $used_ports);
        $used_ports = array_unique($used_ports, SORT_STRING);
        // Check port range to find one free
        $this->Machinonip = new Machinonip();
        do {
            $next_port = rand($tunnel_ports[0], $tunnel_ports[1]);
            $tunnel_used = $this->Machinonip->findFirstBy('tunnel_port', $next_port);
            if ($tunnel_used) {
            } else if (!in_array($next_port, $used_ports)) {
                // Tries to connect to port to be 100% sure
                $fp = @fsockopen("127.0.0.1", $next_port, $errno, $errstr, 30);
                // If TCP connection fails, port is available
                if (!$fp) {
                    $this->tunnel_port = $next_port;
                    $port_found = true;
                }
            } else {
                array_push($used_ports, $next_port);
                $tryout_counter++;
            }
        } while (!$port_found && $tryout_counter > 60); // After 60 tries, something happens....
        // No ports available, return error...
        if (!$port_found)  {
            $this->flash_custom('Unable to open link - Please try again in a minute', 'error');
        }
        // Generating new hash
        $this->tunnel_hash = Le::generate_hash(8); // Will return a 16 length hash
        $this->tunnel_pin = Le::generate_pin(6); // Will return a 6 digit pin length hash
        // Remove old hashes using the same port, as everything must be unique...
        $this->Machinonip->destroyAll(array('device_id = ? OR tunnel_port = ? OR tunnel_hash = ?',
            $this->device->id, $this->tunnel_port, $this->tunnel_hash));
        // crear registro en machinonip con deviceid, hash, pin y puerto
        $this->machinonip = new Machinonip();
        $this->machinonip->setAttributes(array(
            'device_id' => $this->device->id,
            'tunnel_hash' => $this->tunnel_hash,
            // Apache htpasswd encryption - http://httpd.apache.org/docs/2.2/misc/password_encryptions.html
            'tunnel_pin' => $this->tunnel_pin,
            'tunnel_auth' => base64_encode(sha1($this->tunnel_pin, TRUE)), // prefix '{SHA}' for apache auth deprecated
            'tunnel_port' => $this->tunnel_port,
            'updated_at' => Ak::getDate()
        ));
        if (!$this->machinonip->save()) {
            $this->flash_custom('Error 001 establishing the link - Contact Logic Energy', 'error');
        }
        // Updating the domoproxy file
        $newset = '';
        $domoproxy_file = $_SERVER['DOCUMENT_ROOT'].DS.'..'.DS.'domoproxy';
        // Removing any hash using the same current port
        $handle = fopen($domoproxy_file, 'r');
        while (!feof($handle)) {
            $line = fgets($handle, 4096);
            if (!empty($line) && !preg_match("/(?:{$this->tunnel_hash}|\\s{$this->tunnel_port})/", $line)) {
                $newset .= $line;
            }
        }
        fclose($handle);
        $handle = fopen($domoproxy_file, 'w');
        // Add cleanup set content to domoproxy file
        fwrite($handle, $newset);
        // Add the current line to domoproxy file
        fwrite($handle, $this->tunnel_hash . ' ' . $this->tunnel_port . "\n");
        fclose($handle);
        // enviar mensaje al mqtt
        require(AK_APP_VENDOR_DIR.DS.'phpMQTT'.DS.'phpMQTT.php');
        $server = LE_MQTT_SERVER_HOST;
        $port = LE_MQTT_SERVER_PORT;
        $username = LE_MQTT_SERVER_USER;
        $password = LE_MQTT_SERVER_PASS;
        $client_id = LE_MQTT_SERVER_CLIENTID;
        $mqtt = new \Bluerhinos\phpMQTT($server, $port, $client_id);
        // MQTT Topic
        $mqtt_topic = "remote/" . strtolower($this->device->mac_address);
        $mqtt_mesagge = json_encode(array(
            'tunnel' => 'open',
            'port' => (string)$this->tunnel_port,
            'device_id' => $this->device->id
        ));
        // Try to send the message up to 5 times...
        $tryagain = 1;
        do {
            if ($mqtt->connect(true, NULL, $username, $password)) {
                if (LE_SERVER_HOST != 'dev.darkase.org') {
                    $mqtt->publish($mqtt_topic, $mqtt_mesagge, 0);
                }
                $mqtt->close();
                $tryagain = 10;
            } else {
                sleep(1);
                $tryagain++;
            }
        } while ($tryagain <= 5);


        return back()->with('error', 'Unable to establish tunnel');
    }

    /**
     * Closes the tunnel with the remote device
     *
     * @param DeviceTunnel $tunnel
     * @return \Illuminate\Http\Response
     */
    public function disconnect($id)
    {
        $device_tunnel = DeviceTunnel::find($id);
        $this->authorize('connect', $device_tunnel);
        return "hola ke ase";
    }
}
