<?
namespace msmvc\help;

/**
 * Tools
 * unsorted methods, that easy hard gray life
 */
abstract class xhelp {
	/**
	 * Copy folder with it subfolders
	 * @param string $from_path
	 * @param string $to_path
	 */
	static public function copy_dir($from_path, $to_path) {
		mkdir($to_path, 0777);

		if (is_dir($from_path)) {
			chdir($from_path);
			$handle = opendir('.');
			while (($file = readdir($handle)) !== false)
			{
				if (($file != ".") && ($file != ".."))
				{
					if (is_dir($file))
					{
						rec_copy($from_path.$file."/", $to_path.$file."/");
						chdir($from_path);
					}
				}
				if (is_file($file))
				{
					copy($from_path.$file, $to_path.$file);
				}
			}
		}

		if (!empty($handle)) {
			closedir($handle);
		}
	}
}
?>