role :app, "rootbeer-1", "moxie-1" # Both
#role :app, "rootbeer-1"              # JUST ICTC
#role :app, "moxie-1"          # JUST MACC

set :deploy_via, :copy
set :deploy_to, "/htapps/catalog"



