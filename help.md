*parameter formats*

- fname: capitalized
- lname: capitalized
- start: yyyy-mm-dd
- end: yyyy-mm-dd
- savevar: key=value,nextkey=nextvalue
- delvar

parameters are optional but shorten the interactive part of the script

*delvar*

type: toggle
used to delete store.json containing saved vars
usage: --delvar

*savevar*

type: string
saves commaseperated key=value pairs to store.json
available keys: ["lname", "fname"]
vars will be used in case no option for given var is specified in other arguments
usage: --savevar="lname=Hammer,fname=Paul"
