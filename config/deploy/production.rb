# role :app, "moxie-1", "lassi" # Both
role :app, "lassi"              # JUST ICTC
role :app, "moxie-1"          # JUST MACC

set :deploy_via, :copy
set :deploy_to, "/htapps/catalog"



