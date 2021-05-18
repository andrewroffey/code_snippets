#!/usr/bin/env python

import re
import json

def dmr2json(dmr):
  """ dmr2json(str) => json object

  Convert WildFly DMR output to JSON. Not necessary in WildFly 12+ which
  can directly output JSON. """
  jsonstr = dmr
  ## quick and dirty conversion
  ## look away if you were looking for a real parser
  jsonstr = re.sub(r"=> *(undefined|true|false)", r': "\g<1>"', jsonstr)
  jsonstr = re.sub(r"=>", r":", jsonstr)
  jsonstr = re.sub(r"\((.*):(.*)\)(,)?", r"{\g<1>:\g<2>}\g<3>", jsonstr)
  jsonstr = re.sub(r'expression "(.*)"', r'"expression \g<1>"', jsonstr)
  jsonstr = re.sub(r"([0-9])L", r"\g<1>", jsonstr)
  return json.loads(jsonstr)

