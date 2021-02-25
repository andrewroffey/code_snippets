import os
import re

def parseSSHConfig():
  """ parseSSHConfig() -> dict

  read $HOME/.ssh/config and /etc/ssh/ssh_config; return multidimensional
  dict host => directive => values

  For example, /etc/ssh/ssh_config with contents

    Host MyHost
      User root
      Port 22

  would return:  
  {
    "MyHost": {
      "user": "root",
      "port": 22
    }
  }
  """
  config = {} # host => directive => values
  directiveMatch = re.compile("^\s*([^\s]+)\s+(.*)\s*$")
  userConfig = None if "HOME" not in os.environ else \
    os.path.join(os.environ["HOME"], ".ssh", "config")
  systemConfig = "/etc/ssh/ssh_config"

  for configFn in [systemConfig, userConfig]:
    if not configFn:
      continue
    curHosts = []
    try:
      with open(configFn, "r") as fh:
        for line in fh.readlines():
          if line.strip().startswith("#") or not line.strip():
            continue
          matches = directiveMatch.match(line)
          if not matches:
            continue
          directive = matches.group(1).lower()
          value = matches.group(2)
          if directive == "host":
            ## Host directive may contain multiple hostnames
            curHosts = value.split()
            for curHost in curHosts:
              if curHost not in config:
                config[curHost] = {}
          elif not curHosts:
            ## can't set a directive outside of a Host block
            continue
          else:
            ## apply directive to host(s)
            for curHost in curHosts:
              config[curHost][directive] = value
    except:
      continue # couldn't read ssh config file

  ## any directives in * host will be applied to all hosts that the
  ## directive hasn't been set on
  if "*" in config:
    for host in config:
      if host == "*":
        continue
      for directive, value in config["*"].items():
        if directive not in config[host]:
          config[host][directive] = value

    ## don't need wildcard host anymore
    del config["*"]

  return config

