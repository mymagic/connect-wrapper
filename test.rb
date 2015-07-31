require 'mcrypt'
require 'openssl'

plaintext = 'shauleong@gmail.com'
puts plaintext

key = 'thisisaveryawesomemagicsalt12345'

enc = Mcrypt.new(:rijndael_256, :ecb, key, nil, :zeros)
encrypted = enc.encrypt(plaintext)

puts Digest::MD5.hexdigest(encrypted)
