<html>
<head>
    <title>PHP Source Highlighter</title>
    <style type="text/css">
        body { color: #eee; background-color: #0a0a0a; }
        pre { font: 400 14px 'Consolas', 'Bitstream Vera Sans Mono', 'Druid Mono', monospace; color: #fff; }
        span.string   { color: #6df; }
        span.variable { color: #fd3; }
        span.constant { color: #f50; }
        span.modifier { color: #fa7; font-style: italic; }
        span.storage { color: #fa7; font-weight: 800; }
        span.comment { color: #aaa; }
    </style>
</head>
<body>
<?php

/**
 * Formats source code with HTML markup for syntax highlighting with CSS.
 *
 * @author     Adrian Unger <unger.adrian@gmail.com>
 * @link       http://staydecent.ca
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @version    0.0.2
 */
 
class Highlighter {

    private $defined_functions;
    
    function __construct() {
        $this->defined_functions = get_defined_functions();
    }
    
    function format( $file, $lang = 'php' ) {
        // Read from file or string
        if( is_file($file) ) {
            $source = strip_tags(file_get_contents($file));
        }
        else {
            $source = strip_tags($file);
        }
        
        // Convert line-breaks
        $source = preg_replace('/\r\n|\r/', "\r\n", $source);
        
        // Read line by line
        $lines = explode("\n", $source);
        $n_lines = count($lines);
        
        for( $i = 0; $i < $n_lines; ++$i ) {          
            $first_char = substr(trim($lines[$i]), 0, 1);
            
            if( $first_char == '/' || $first_char == '*' || $first_char == '#' ) {
                $lines[$i] = '<span class="comment">' . $lines[$i] . '</span>';
            }
            else {
                // Strings(quoted)
                $lines[$i] = preg_replace('/(".*?"|\'.*?\')/', '<span class="string">$1</span>', $lines[$i]);

                // Variables
                $lines[$i] = preg_replace('/(\$[a-zA-z0-9]*|->[a-zA-z0-9]*)\b/', '<span class="variable">$1</span>', $lines[$i]);

                // Constant
                $lines[$i] = preg_replace('/(__[a-zA-Z0-9]*_+|\$_+[A-Z]*)/', '<span class="constant">$1</span>', $lines[$i]);

                // Storage
                $lines[$i] = preg_replace('/(class|function|new|public|private|protected|var|interface) /', '<span class="storage">$1</span> ', $lines[$i]);

                // Storage Modifier
                $lines[$i] = preg_replace('/(abstract|extends|implements) /', '<span class="modifier">$1</span> ', $lines[$i]);
            }
        }
    
        $output = '';

        foreach( $lines as $line ) {
            if( !empty($line) ) {
                $output .= $line . "\r\n";
            }
        }

        return "<pre class=\"$lang\">\n" . $output . "</pre>\n";
    }
}

$hl = new Highlighter;
echo $hl->format('
/**
 * Class Name
 * Description
 * $var
 * @PHPDoc tags
 */
class ClassName extends AnotherClass
{
    function __construct($param)
    {
        # code...
        $classclassclassfunction
        //functionclass
        $this->route = (empty($_GET[\'route\'])) ? $this->c_default : trim($_GET[\'route\'], \'/\\\');
    }
    
    public function FunctionName($value)
    {
        echo __FILE__, "String";
    }
}
');
?>
</body>
</html>